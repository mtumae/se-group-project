import { createFileRoute } from '@tanstack/react-router'
import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
export const Route = createFileRoute('/admin')({
  component: RouteComponent,
})

function RouteComponent() {
    const {data:users, isLoading:usersLoading}=useQuery(convexQuery(api.users.getAllUsers, {}));
  return (
    <div>
        <h1 className='text-xl font-semibold'>Users</h1>
        {usersLoading ? (
          <p>Loading...</p>
        ) : (
          <ul>
            {users?
            users.map((user) => (
              <li key={user._id}>{user.email}</li>
            )) : null}
          </ul>
        )}
    </div>
  )
}
