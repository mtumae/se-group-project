import { createFileRoute } from '@tanstack/react-router'
import {
  useQuery,
  useMutation,
  useQueryClient,
  QueryClient,
  QueryClientProvider,
} from '@tanstack/react-query'
import  getUsers from '../../server'

export const Route = createFileRoute('/market')({
  component: RouteComponent,
})

function RouteComponent() {
  const data = useQuery({queryKey:['users'], queryFn:getUsers})
  return (
  <div >
    <h1>Marketplace</h1>
    <div className='grid gap-3'>
      {data.isFetching?
      <div>Loading...</div>
      :data.isError?
      <div>Error...</div>
      :data.data?.map((d)=>(
        <div key={d.id}>
          <p className='font-bold'>{d.name}</p>
          <p className='text-xs text-gray-40'>{d.email}</p>
        </div>
      ))
    }
    </div>
  </div>
  )
}
